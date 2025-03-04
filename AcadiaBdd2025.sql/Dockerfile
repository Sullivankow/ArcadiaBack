# Utiliser l'image officielle de MySQL comme base
FROM mysql:8.0

# Définir le mot de passe root par défaut
ENV MYSQL_ROOT_PASSWORD=Madinalake_3

# Définir le nom de la base de données par défaut
ENV MYSQL_DATABASE=arcadia

# Copier le dump de la base de données dans le conteneur
COPY backup.sql /docker-entrypoint-initdb.d/

# Exposer le port MySQL par défaut
EXPOSE 3306
