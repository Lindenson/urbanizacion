#!/usr/bin/env bash
# Exporta el contenido actual del sitio para migrarlo a otro WordPress.
# Genera en ./backup/:
#   - urbanizacion-db.sql : base de datos completa (publicaciones, páginas,
#                           usuarios, ajustes, menú, foro...).
#   - uploads.tgz         : archivos subidos (fotos, PDF...).
#
# Requiere que el stack esté en marcha (docker compose up -d).
set -euo pipefail

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
cd "$ROOT"
OUT="$ROOT/backup"
mkdir -p "$OUT"

echo "1/2  Exportando la base de datos..."
docker compose exec -T db sh -c \
  'exec mysqldump --no-tablespaces -u"$MYSQL_USER" -p"$MYSQL_PASSWORD" "$MYSQL_DATABASE"' \
  > "$OUT/urbanizacion-db.sql"

echo "2/2  Empaquetando los archivos subidos (uploads)..."
docker compose exec -T wordpress tar czf /tmp/_uploads.tgz -C /var/www/html/wp-content uploads
docker cp urbanizacion-wp:/tmp/_uploads.tgz "$OUT/uploads.tgz"
docker compose exec -T wordpress rm -f /tmp/_uploads.tgz

echo
echo "Backup creado en backup/:"
ls -lh "$OUT"
echo
echo "NO subas esta carpeta a Git (contiene datos de usuarios)."
