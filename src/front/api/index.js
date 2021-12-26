import {tokenHeader, tokenUrl} from "@/api/constants";


export function getToken({login, password}) {
  let options = {
    method: 'GET',
    headers: {'Content-Type': 'application/json;charset=utf-8'},
  };
  let params = new URLSearchParams();
  params.append('login', login);
  params.append('password', password);
  return fetch(`${tokenUrl}?${params.toString()}`, options).then(response => response.headers.get(tokenHeader));
}
