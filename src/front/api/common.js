import {tokenHeader} from "@/api/constants";


export class BadStatusError extends Error {
  constructor(status) {
    super('Bad status: ' + status);
    this.status = status;
  }
}

/**
 *
 * @param {Response} response
 * @returns {Promise<*>}
 */
export async function parseBody(response) {
  if (response.ok) {
    return (await response.json()).data;
  }
  throw await response.json()
    .then(json => new BadStatusError(response.status, json.data))
    .catch(() => new BadStatusError(response.status));
}

export function tee(target) {
  if (typeof target === 'function') return realTarget => {
    console.log(target(realTarget));
    return realTarget;
  }
  console.log(target);
  return target;
}

/**
 *
 * @param {string} url
 * @param token
 * @param cls
 * @returns {Promise<Object[]>}
 */
export function getAllEntities({url, token, cls}) {
  return fetch(url, {headers: {[tokenHeader]: token}})
    .then(parseBody)
    .then(entities => entities.map(entity => new cls(entity)));
}

export function getEntity({url, token, cls}) {
  return fetch(url, {headers: {[tokenHeader]: token}})
    .then(parseBody)
    .then(entity => new cls(entity));
}

/**
 *
 * @param {string} url
 * @param {string} body
 * @param token
 * @param cls
 * @returns {Promise<Object>}
 */
export function createEntity({url, body, token, cls}) {
  let options = {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json;charset=utf-8',
      [tokenHeader]: token,
    },
    body
  };
  return fetch(url, options)
    .then(parseBody)
    .then(entity => new cls(entity));
}

/**
 *
 * @param {string} url
 * @param {string} body
 * @param token
 * @param cls
 * @returns {Promise<Object>}
 */
export function updateEntity({url, body, token, cls}) {
  let options = {
    method: 'PUT',
    headers: {
      'Content-Type': 'application/json;charset=utf-8',
      [tokenHeader]: token,
    },
    body
  };
  return fetch(url, options)
    .then(response => new Promise(async (resolve, reject) => {
        if (response.ok) {
          resolve(new cls(await parseBody(response)));
        } else {
          reject(new cls(await parseBody(response)));
        }
      })
    );
}

/**
 *
 * @param {string} url
 * @param token
 * @returns {Promise<Response>}
 */
export function deleteEntity({url, token}) {
  return fetch(url, {
    method: 'DELETE',
    headers: {
      [tokenHeader]: token
    }
  });
}