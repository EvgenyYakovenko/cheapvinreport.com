#!/usr/bin/env bash

set -u

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$ROOT_DIR" || exit 1

print_title() {
  echo
  echo "=== $1 ==="
}

exists() {
  command -v "$1" >/dev/null 2>&1
}

mask() {
  local v="$1"
  local len=${#v}
  if [[ "$len" -eq 0 ]]; then
    echo "(empty)"
    return
  fi
  if [[ "$len" -le 2 ]]; then
    printf '%*s\n' "$len" '' | tr ' ' '*'
    return
  fi
  local first="${v:0:1}"
  local last="${v: -1}"
  local middle_len=$((len - 2))
  local middle
  middle=$(printf '%*s' "$middle_len" '' | tr ' ' '*')
  echo "${first}${middle}${last}"
}

env_value() {
  local key="$1"
  if [[ -f ".env" ]]; then
    local line
    line=$(grep -E "^${key}=" .env | tail -n 1 || true)
    if [[ -n "$line" ]]; then
      echo "${line#*=}"
      return
    fi
  fi
  echo ""
}

print_title "Environment"
redis_host="$(env_value REDIS_HOST)"
redis_port="$(env_value REDIS_PORT)"
redis_password="$(env_value REDIS_PASSWORD)"
redis_client="$(env_value REDIS_CLIENT)"
cache_store="$(env_value CACHE_STORE)"

echo "REDIS_CLIENT: ${redis_client:-"(empty)"}"
echo "REDIS_HOST: ${redis_host:-"(empty)"}"
echo "REDIS_PORT: ${redis_port:-"(empty)"}"
echo "REDIS_PASSWORD: $(mask "${redis_password:-}")"
echo "CACHE_STORE: ${cache_store:-"(empty)"}"

print_title "System Binaries"
if exists redis-server; then
  echo "redis-server: $(command -v redis-server)"
else
  echo "redis-server: not found"
fi

if exists redis-cli; then
  echo "redis-cli: $(command -v redis-cli)"
else
  echo "redis-cli: not found"
fi

if exists php; then
  echo "php: $(command -v php)"
  php -v | head -n 1
else
  echo "php: not found"
fi

print_title "Port Check"
host="${redis_host:-127.0.0.1}"
port="${redis_port:-6379}"

if exists nc; then
  if nc -z "$host" "$port" >/dev/null 2>&1; then
    echo "tcp://${host}:${port}: OPEN"
  else
    echo "tcp://${host}:${port}: CLOSED/UNREACHABLE"
  fi
else
  echo "nc: not found (skip tcp check)"
fi

print_title "PHP Redis Capabilities"
if exists php; then
  if php -m | grep -qi '^redis$'; then
    echo "phpredis extension: loaded"
  else
    echo "phpredis extension: NOT loaded"
  fi

  if php -r 'require "vendor/autoload.php"; echo class_exists("Predis\\Client") ? "yes\n" : "no\n";' 2>/dev/null | grep -q '^yes$'; then
    echo "predis package: installed"
  else
    echo "predis package: NOT installed"
  fi
fi

print_title "redis-cli Ping"
if exists redis-cli; then
  if [[ -n "${redis_password}" && "${redis_password}" != "null" ]]; then
    redis-cli -h "$host" -p "$port" -a "$redis_password" ping || true
  else
    redis-cli -h "$host" -p "$port" ping || true
  fi
else
  echo "skip (redis-cli not found)"
fi

print_title "Laravel Runtime Check"
if exists php; then
  php -d error_reporting='E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED' scripts/check-laravel-redis.php
else
  echo "skip (php not found)"
fi

echo
echo "Finished."
