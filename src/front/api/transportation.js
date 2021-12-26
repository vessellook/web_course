import {createEntity, deleteEntity, getAllEntities, getEntity, updateEntity} from "@/api/common";
import {orderUrl, transportationUrl} from "@/api/constants";
import Transportation from "@/models/Transportation";

let cls = Transportation;

/**
 *
 * @param orderId
 * @param token
 * @returns {Promise<Transportation[]>}
 */
export function getTransportations({orderId = null, token}) {
  let url = (orderId === null) ? transportationUrl : `${orderUrl}/${orderId}`;
  return getAllEntities({url, token, cls});
}

export function getTransportation({transportationId, token}) {
  return getEntity({
    url: `${transportationUrl}/${transportationId}`,
    token
  });
}

/**
 *
 * @param orderId
 * @param {Transportation} transportation
 * @param token
 * @returns {Promise<Transportation>}
 */
export function createTransportation({orderId, transportation, token}) {
  return createEntity({
    url: `${orderUrl}/${orderId}`,
    body: JSON.stringify(transportation),
    token,
    cls
  });
}

export function updateTransportation({transportationId, oldTransportation, newTransportation, token}) {
  return updateEntity({
    url: `${transportationUrl}/${transportationId}`,
    body: JSON.stringify({old: oldTransportation, new: newTransportation}),
    token,
    cls
  });
}

/**
 *
 * @param transportationId
 * @param token
 * @returns {Promise<Response>}
 */
export function deleteTransportation({transportationId, token}) {
  return deleteEntity({
    url: `${transportationUrl}/${transportationId}`,
    token
  });
}