import {tokenUrl, tokenHeader} from "@/api/constants";
import {tee} from "@/api/common";


export function getToken({login, password}) {
  let params = new URLSearchParams();
  params.append('login', login);
  params.append('password', password);
  return fetch(`${tokenUrl}?${params.toString()}`)
    .then(response => response.headers.get(tokenHeader));
}

export function updateToken({token}) {
  return fetch(`${tokenUrl}/update`, {headers: {[tokenHeader]: token}})
    .then(response => response.headers.get(tokenHeader));
}
