import {createEntity, deleteEntity, getAllEntities, getEntity, updateEntity} from "@/api/common";
import {customerUrl} from "@/api/constants";
import Customer from "@/models/Customer";

let cls = Customer;

/**
 *
 * @param token
 * @returns {Promise<Customer[]>}
 */
export function getCustomers({token}) {
  return getAllEntities({
    url: customerUrl,
    token,
    cls
  });
}

export function getCustomer({customerId, token}) {
  return getEntity({
    url: `${customerUrl}/${customerId}`,
    token,
    cls
  });
}

/**
 *
 * @param {Customer} customer
 * @param token
 * @returns {Promise<Customer>}
 */
export function createCustomer({customer, token}) {
  return createEntity({
    url: customerUrl,
    body: JSON.stringify(customer),
    token,
    cls
  });
}

/**
 *
 * @param customerId
 * @param {Customer} oldCustomer
 * @param {Customer} newCustomer
 * @param token
 * @returns {Promise<Customer>}
 */
export function updateCustomer({customerId, oldCustomer, newCustomer, token}) {
  return updateEntity({
    url: `${customerUrl}/${customerId}`,
    body: JSON.stringify({old: oldCustomer, new: newCustomer}),
    token,
    cls
  });
}

/**
 *
 * @param customerId
 * @param token
 * @returns {Promise<Response>}
 */
export function deleteCustomer({customerId, token}) {
  return deleteEntity({url: `${customerUrl}/${customerId}`, token})
}