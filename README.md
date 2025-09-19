# ytdl-sub Manager

## About

Web-based utility for managing the `subscriptions.yaml` file used by ytdl-sub. This allows you to add new subscriptions, erase existing subscriptions, and change the preset/URL for existing subscriptions without needing to SSH into the Docker host and manually edit the yaml file.

## Usage

Create `APP_KEY` environment variable:
```bash
echo "APP_KEY=$(docker run --rm --entrypoint php ghcr.io/rpungello/ytdl-sub-manager:latest artisan key:generate --show)" >.env
```

### Docker

```bash
docker run -d \
  --name ytdl-sub-manager \
  -p 80:80 \
  -v /path/to/ytdl-config:/config \
  -v /path/to/downloaded/videos:/videos \
  --env-file .env \
  ghcr.io/rpungello/ytdl-sub-manager:latest
```

### Docker Compose

Download standard `docker-compose.yml` file:
```bash
wget -O docker-compose.yml https://raw.githubusercontent.com/rpungello/ytdl-sub-manager/refs/heads/main/docker-compose-prod.yml
```

Modify this file to have the correct path that contains `config.yaml` and `subscriptions.yaml` for ytdl-sub mapped to `/config`.

If you want to use a port other than 80, set a `WEB_PORT` environment variable.

Start the container:
```bash
docker-compose up -d
```
