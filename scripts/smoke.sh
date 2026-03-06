#!/usr/bin/env bash

set -euo pipefail

BASE_URL="${1:-${BASE_URL:-http://127.0.0.1:9091}}"
TMP_BODY="$(mktemp)"

cleanup() {
  rm -f "$TMP_BODY"
}
trap cleanup EXIT

require_command() {
  local command="$1"
  if ! command -v "$command" >/dev/null 2>&1; then
    printf 'Missing required command: %s\n' "$command" >&2
    exit 1
  fi
}

request() {
  local method="$1"
  local path="$2"
  local payload="$3"
  local expected_status="$4"
  local expected_fragment="$5"

  local status_code

  if [[ -n "$payload" ]]; then
    if ! status_code="$(
      curl -sS -o "$TMP_BODY" -w "%{http_code}" \
        -X "$method" "$BASE_URL$path" \
        -H 'Content-Type: application/json' \
        --data "$payload"
    )"; then
      printf 'Request failed: %s %s\n' "$method" "$path" >&2
      exit 1
    fi
  else
    if ! status_code="$(
      curl -sS -o "$TMP_BODY" -w "%{http_code}" \
        -X "$method" "$BASE_URL$path"
    )"; then
      printf 'Request failed: %s %s\n' "$method" "$path" >&2
      exit 1
    fi
  fi

  if [[ "$status_code" != "$expected_status" ]]; then
    printf 'Unexpected status for %s %s. Expected %s, got %s.\n' \
      "$method" "$path" "$expected_status" "$status_code" >&2
    printf 'Response body:\n' >&2
    cat "$TMP_BODY" >&2
    printf '\n' >&2
    exit 1
  fi

  if [[ -n "$expected_fragment" ]] && ! grep -Fq "$expected_fragment" "$TMP_BODY"; then
    printf 'Unexpected body for %s %s. Missing fragment: %s\n' \
      "$method" "$path" "$expected_fragment" >&2
    printf 'Response body:\n' >&2
    cat "$TMP_BODY" >&2
    printf '\n' >&2
    exit 1
  fi

  printf '%s %s -> %s\n' "$method" "$path" "$status_code"
}

require_command curl

printf 'Running smoke test against %s\n' "$BASE_URL"

request "GET" "/status" "" "200" "\"status\":\"ok\""
request "PUT" "/cars" '[{"id":1,"seats":4},{"id":2,"seats":6}]' "200" "\"fleet_replaced\""
request "POST" "/journey" '{"id":101,"people":4}' "200" "\"assigned\""
request "POST" "/locate" '{"id":101}' "200" "\"car_id\":1"
request "POST" "/journey" '{"id":102,"people":6}' "200" "\"car_id\":2"
request "POST" "/journey" '{"id":103,"people":2}' "202" "\"waiting\""
request "POST" "/locate" '{"id":103}' "204" ""
request "POST" "/dropoff" '{"id":102}' "200" "\"dropped_off\""
request "POST" "/locate" '{"id":103}' "200" "\"assigned\""

printf 'Smoke test completed successfully.\n'
