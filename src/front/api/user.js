import {userUrl} from "@/api/constants";
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

export function getUser({userId, token}) {
  return getEntity({
    url: `${userUrl}/${userId}`,
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

export function updateUser({userId, oldUser, newUser, token}) {
  return updateEntity({
    url: `${userUrl}/${userId}`,
    body: JSON.stringify({old: oldUser, new: newUser}),
    token,
    cls
  });
}

export function deleteUser({userId, token}) {
  return deleteEntity({url: `${userUrl}/${userId}`, token});
}
