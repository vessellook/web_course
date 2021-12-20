import Product from "@/models/Product";

const baseUrl = `http://${process.env.SERVER_NAME}:${process.env.SITE_PORT}`;
const apiUrl = `${baseUrl}/api/v1`;
const userUrl = `${apiUrl}/users`;
const productUrl = `${apiUrl}/products`;
const tokenUrl = `${apiUrl}/token`;
const updateTokenUrl = `${apiUrl}/token#update`
const orderUrl = `${apiUrl}/order`;
const tokenHeader = 'SESSION-TOKEN';

async function parseBody(response) {
  return (await response.json()).data;
}

function tee(target) {
  if(typeof target === 'function') return realTarget => {
    console.log(target(realTarget));
    return realTarget;
  }
  console.log(target);
  return target;
}


export function registerUser(properties) {
  let options = {
    method: 'POST',
    headers: {'Content-Type': 'application/json;charset=utf-8'},
    body: JSON.stringify(properties)
  };
  return fetch(userUrl, options).then(parseBody);
}

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

/**
 *
 * @returns {Promise<Product[]>}
 */
export function fetchProducts() {
  return fetch(productUrl)
    .then(parseBody)
    .then(products => products.map(product => new Product(product)));
}

export function createProduct({token, product}) {
  let options = {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json;charset=utf-8',
      tokenHeader: token,
    },
    body: JSON.stringify(product)
  };
  return fetch(productUrl, options).then(parseBody);
}

export function getOrders({token}) {
  return fetch(orderUrl, {headers: {tokenHeader: token}}).then(parseBody);
}

export function createOrder({order, token}) {
  let options = {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json;charset=utf-8',
      tokenHeader: token,
    },
    body: JSON.stringify(order)
  };
  return fetch(orderUrl, options).then(parseBody);
}

export function addItemToOrder({order, item, token}) {
  let options = {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json;charset=utf-8',
      tokenHeader: token,
    },
    body: JSON.stringify(item)
  };
  return fetch(`${orderUrl}/${order.id}/items`, options).then(parseBody);
}

export function removeItemFromOrder({order, item, token}) {
  let options = {
    method: 'DELETE',
    headers: {
      tokenHeader: token,
    }
  };
  return fetch(`${orderUrl}/${order.id}/items/${item.id}`, options).then(parseBody);
}
