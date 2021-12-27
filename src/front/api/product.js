import {productUrl} from "@/api/constants";
import Product from "@/models/Product";
import {getAllEntities, getEntity} from "@/api/common";

let cls = Product;

/**
 *
 * @returns {Promise<Product[]>}
 */
export function getProducts({token}) {
  return getAllEntities({
    url: productUrl,
    token,
    cls
  });
}

export function getProduct({productId, token}) {
  return getEntity({
    url: `${productUrl}/${productId}`,
    token,
    cls
  });
}