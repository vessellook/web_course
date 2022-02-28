import {tokenHeader} from "@/api/constants";


export class BadStatusError extends Error {
  constructor(status, data) {
    super('Bad status: ' + status);
    this.status = status;
    this.data = data;
  }
}

/**
 *
 * @param {Response} response
 * @returns {Promise<*>}
 */
export async function parseBody(response) {
  if (response.ok) {
    let body = (await response.text());
    let pos = body.indexOf('}{') + 1;
    if(pos > 0) {
      body = body.slice(pos);
    }
    return JSON.parse(body).data;
  }
  throw await response.text()
    .then(body => {
      let pos = body.indexOf('}{') + 1;
      if(pos > 0) {
        body = body.slice(pos);
      }
      return new BadStatusError(response.status, JSON.parse(body).data)
    })
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
    .then(entity => new cls(entity), error => {
      error.data = new cls(error.data);
      return error;
    });
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
  return fetch(url, options).then(parseBody)
    .then(entity => new cls(entity), error => {
      error.data = new cls(error.data);
      throw error;
    });
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