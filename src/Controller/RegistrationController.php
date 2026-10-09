<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Security\EmailVerifier;
use App\DTO\User\RegistrationDto;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use Symfony\Component\Mime\Address;
use Symfony\Component\Form\FormError;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    public function __construct(
        private EmailVerifier $emailVerifier,
        private UserRepository $userRepository,
        private string $mailerFromAddress,
        private string $mailerFromName,
    ) {
    }

    #[Route('/inscription', name: 'app_register', methods: ['GET', 'POST'])]
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager,
    ): Response {
        if ($this->getUser()) {
            $this->addFlash('info', 'Vous êtes déjà connecté.');

            return $this->redirectToRoute('homepage');
        }

        $registrationDto = new RegistrationDto();
        $form = $this->createForm(RegistrationFormType::class, $registrationDto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $existingUser = $this->userRepository->findOneBy(['email' => $registrationDto->email]);
            if ($existingUser) {
                $form->get('email')->addError(new FormError('Cette adresse email est déjà utilisée.'));

                return $this->render('registration/register.html.twig', ['registrationForm' => $form]);
            }

            $user = new User();

            $user->setEmail($registrationDto->email);
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $registrationDto->plainPassword,
                ),
            );

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Votre compte a bien été créé. Vous avez reçu un lien pour valider celui-ci dans votre boite mail.');

            $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                (new TemplatedEmail())
                    ->from(new Address($this->mailerFromAddress, $this->mailerFromName))
                    ->to((string) $user->getEmail())
                    ->subject('Please Confirm your Email')
                    ->htmlTemplate('registration/confirmation_email.html.twig'),
            );

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }

    #[Route('/verify/email', name: 'app_verify_email', methods: ['GET', 'POST'])]
    public function verifyUserEmail(Request $request, TranslatorInterface $translator): Response
    {
        $id = $request->query->get('id');
        if (null === $id) {
            $this->addFlash('error', "Votre adresse email n'a pas pu être vérifiée.");

            return $this->redirectToRoute('app_register');
        }

        $user = $this->userRepository->find($id);
        if (null === $user) {
            $this->addFlash('error', "Votre adresse email n'a pas pu être vérifiée.");

            return $this->redirectToRoute('app_register');
        }

        try {
            $this->emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash(
                'verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'),
            );

            return $this->redirectToRoute('app_register');
        }

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash('success', 'Votre adresse email a bien été vérifiée.');

        return $this->redirectToRoute('app_login');
    }
}
