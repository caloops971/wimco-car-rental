# Wimco Car Rental Website

Un site web de location de voitures pour St. Barthélemy avec intégration API RentCentric et validation complète des formulaires.

## 🚗 Fonctionnalités

- **Recherche de véhicules** : Interface intuitive pour rechercher des véhicules par dates
- **Intégration API** : Connexion avec l'API RentCentric pour récupérer les véhicules disponibles
- **Réservations en ligne** : Système complet de réservation avec validation
- **Design responsive** : Interface moderne et adaptée à tous les écrans
- **Branding personnalisé** : Couleurs et logos Wimco intégrés

## 🔧 Technologies

- **Frontend** : HTML5, CSS3, JavaScript (ES6+)
- **Backend** : PHP 8.2
- **API** : RentCentric Integration
- **Serveur de développement** : PHP Built-in Server
- **Outils** : GitHub CLI, Git

## 📋 Validation des formulaires

### Email
- ✅ Format email valide
- ❌ Domaines interdits : @wimco, @stbarth

### Téléphone
- ✅ Chiffres uniquement (0-9)
- ✅ + optionnel en début
- ✅ Minimum 8 caractères

### Code Villa / Livraison
- ✅ Champ obligatoire
- ✅ Minimum 3 caractères

## 🚀 Installation et démarrage

### Prérequis
- PHP 8.2 ou supérieur
- XAMPP (recommandé pour Windows)

### Démarrage rapide
1. Cloner le repository
```bash
git clone https://github.com/caloops971/wimco-car-rental.git
cd wimco-car-rental
```

2. Démarrer le serveur de développement
```bash
# Avec PHP intégré
php -S localhost:8080 router.php

# Ou avec XAMPP
# Copier les fichiers dans C:\xampp\htdocs\wimco
# Démarrer Apache depuis le panneau XAMPP
```

3. Ouvrir dans le navigateur
```
http://localhost:8080
```

## 📁 Structure du projet

```
wimco-car-rental/
├── config.php              # Configuration API et partenaire
├── index.php               # Point d'entrée principal
├── template.php             # Template principal avec formulaires
├── create_reservation.php   # Traitement des réservations
├── confirmation.php         # Page de confirmation
├── router.php              # Router pour serveur de développement
├── styles.css              # Styles CSS principaux
├── images/                 # Assets images
│   ├── logos/             # Logos partenaires
│   └── *.jpg              # Images de présentation
└── README.md              # Documentation
```

## 🌐 Déploiement en production

### Configuration Apache
```apache
<VirtualHost *:80>
    ServerName wimco.hertzstbarth.com
    DocumentRoot /path/to/wimco
    
    <Directory /path/to/wimco>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

### Permissions
```bash
find . -type d -exec chmod 755 {} \;
find . -type f -exec chmod 644 {} \;
find . -name "*.php" -exec chmod 755 {} \;
```

### Modules Apache requis
```bash
a2enmod rewrite
a2enmod ssl
service apache2 restart
```

## 🔐 Sécurité

- Validation côté client et serveur
- Tokens API sécurisés
- Validation stricte des entrées utilisateur
- Protection contre les domaines email internes
- SSL recommandé pour la production

## 📞 Support

Pour toute question technique, consultez le code source ou ouvrez une issue sur GitHub.

---

**Développé avec ❤️ pour Wimco Properties**