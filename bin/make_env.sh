#!/usr/bin/env bash

VAULT_LOGIN_URL="${VAULT_URL}/auth/approle/login"
VAULT_SECRET_URL="${VAULT_URL}/sheba/data/partner/${BRANCH_NAME}.env"

VAULT_TOKEN=$(curl --request POST --data '{"role_id":"'"$VAULT_ROLE_ID"'","secret_id":"'"$VAULT_SECRET_ID"'"}' $VAULT_LOGIN_URL \
    | jq -r .auth.client_token)

curl -H "X-Vault-Token: $VAULT_TOKEN" --request GET $VAULT_SECRET_URL \
    | jq -r '.data.data' | jq -r 'to_entries|map("\(.key)=\(.value|tostring)")|.[]' > ${BRANCH_NAME}.env
