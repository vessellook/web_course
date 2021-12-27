import {customerUrl, orderUrl} from "@/api/constants";
import Order from "@/models/Order";
import {createEntity, deleteEntity, getAllEntities, updateEntity} from "@/api/common";

let cls = Order;

/**
 *
 * @param customerId
 * @param token
 * @return {Promise<Order[]>}
 */
export function getOrders({customerId = null, token}) {
  let url = (customerId === null) ? orderUrl : `${customerUrl}/${customerId}/orders`
  return getAllEntities({url, token, cls});
}

export function getOrder({orderId, token}) {
  return getEntity({
    url: `${orderUrl}/${orderId}`,
    token,
    cls: Order
  })
}

/**
 *
 * @param customerId
 * @param {Order} order
 * @param token
 * @returns {Promise<Order>}
 */
export function createOrder({customerId, order, token}) {
  return createEntity({
    url: `${customerUrl}/${customerId}/orders`,
    body: JSON.stringify(order),
    token,
    cls
  });
}

/**
 *
 * @param orderId
 * @param {Order} oldOrder
 * @param {Order} newOrder
 * @param token
 * @returns {Promise<Order>}
 */
export function updateOrder({orderId, oldOrder, newOrder, token}) {
  return updateEntity({
    url: `${orderUrl}/${orderId}`,
    body: JSON.stringify({old: oldOrder, new: newOrder}),
    token,
    cls
  });
}

/**
 *
 * @param orderId
 * @param token
 * @returns {Promise<Response>}
 */
export function deleteOrder({orderId, token}) {
  return deleteEntity({
    url: `${orderUrl}/${orderId}`,
    token
  });
}