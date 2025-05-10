# docker for development

```bash
docker compose up -d
docker exec -it twig-php bash
composer update
exit
docker compose logs -f
```

Browser <http://_IP_:8088>
