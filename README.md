```sh
cp .env.template .env
vi .env
docker run --rm -it -v `pwd`:/app yonex/prestissimo install --no-dev
docker run --rm -it --env-file=.env -v `pwd`:/app -w /app -v /path/to/file:/file php php upload.php /file
```
