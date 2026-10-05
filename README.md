# API Japan

Création d'une API pour un dictionnaire de japonais qui permet de gérer et délivrer les éléments suivants : Kanjis, Kanas et mots de vocabulaire.

## Stack & Outils de développement

### Technologies principales
- **PHP** 8.4+
- **Symfony** 8.1
- **API Platform** 5.0 — Exposition et documentation de l'API REST

### Bundles & Outils de Développement (`require-dev`)

#### Tests & Données
- **DoctrineFixturesBundle** & **Zenstruck Foundry** — Génération et gestion des jeux de données de test (fixtures)
- **DAMA / DoctrineTestBundle** — Isolation des tests dans des transactions de base de données
- **Symfony Test Pack** (`BrowserKit`, `CssSelector`, `HttpClient`) — Tests d'intégration et d'API

#### Qualité de code & Profiling
- **PHPStan** (avec extensions Doctrine & Symfony) — Analyse statique du code
- **PHP-CS-Fixer** — Normalisation du style de code (PSR-12 / Symfony standards)
- **Doctrine Doctor** — Analyse et détection des mauvaises pratiques ORM
- **Web Profiler Bundle** — Barre de débuggage en environnement de dev

---

## Commandes utiles

### Démarrer Docker
```bash
make up
```

### Arrêter Docker
```bash
make down
```

### Création de la base de données
```bash
make db-create
```

### Création de la base de données de test
```bash
make db-test-create
```

### Qualité du code
```bash
# Lancer l'analyse statique PHPStan
vendor/bin/phpstan analyse
```

```bash
# Corriger automatiquement le style de code
vendor/bin/php-cs-fixer fix
```