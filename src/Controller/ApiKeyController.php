<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Form\ApiKeyDtoType;
use App\DTO\ApiKey\ApiKeyDto;
use App\Service\ApiKeyManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/profile')]
final class ApiKeyController extends AbstractController
{
    public function __construct(private ApiKeyManager $apiKeyManager)
    {
    }

    #[Route('/apikeys', name: 'app_apikey')]
    public function index(Request $request): Response
    {
        /** @var User */
        $user = $this->getUser();

        $dto = new ApiKeyDto(user: $user);
        $form = $this->createForm(ApiKeyDtoType::class, $dto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                /** @var User */
                $userDto = $dto->user;
                /** @var string */
                $nameDto = $dto->name;

                $data = $this->apiKeyManager->generate($userDto, $nameDto);
                $this->addFlash('success', sprintf("Votre clé a bien été générée. Gardez-la bien car elle ne sera affichée qu'une seule fois : %s", $data['plainToken']));
            } catch (\Exception $e) {
                $this->addFlash('error', $e->getMessage());
            }

            return $this->redirectToRoute('app_apikey');
        }

        return $this->render('api_key/index.html.twig', [
            'form' => $form,
        ]);
    }
}
