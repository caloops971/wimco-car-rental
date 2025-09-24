# Wimco - Car Rental Website

## Installation
1. Upload all files to your web server
2. Set proper permissions:
   ```bash
   find . -type d -exec chmod 755 {} \;
   find . -type f -exec chmod 644 {} \;
   find . -name "*.php" -exec chmod 755 {} \;
   ```
3. Configure your web server (Apache) virtual host:
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
4. Enable required Apache modules:
   ```bash
   a2enmod rewrite
   a2enmod ssl
   ```
5. Restart Apache:
   ```bash
   service apache2 restart
   ```

## Configuration
- All partner-specific settings are in config.php
- API credentials are already configured
- SSL certificate should be installed for production use

## Support
For support, contact the development team.