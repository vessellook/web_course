import {profileUrl, tokenHeader, userUrl} from "@/api/constants";
import {createEntity, deleteEntity, getAllEntities, getEntity, updateEntity} from "@/api/common";
import User from "@/models/User";

let cls = User;

export function getUsers(token) {
  return getAllEntities({
    url: userUrl,
    token,
    cls
  });
}

export function getUser({userId = null, token}) {
  return getEntity({
    url: userId ? `${userUrl}/${userId}` : profileUrl,
    token,
    cls
  });
}

export function registerUser({user, password, token}) {
  let body = Object.assign({password}, user);
  return createEntity({
    url: userUrl,
    body: JSON.stringify(body),
    token,
    cls
  });
}

export function updateUserPassword({userId = null, password, token}) {
  let url = userId ? `${userUrl}/${userId}` : profileUrl;
  let options = {
    method: 'PATCH',
    headers: {
      'Content-Type': 'application/json',
      [tokenHeader]: token
    },
    body: JSON.stringify({password})
  }
  return fetch(url, options);
}

export function deleteUser({userId, token}) {
  return deleteEntity({url: `${userUrl}/${userId}`, token});
}
