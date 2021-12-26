const baseUrl = `http://${process.env.SERVER_NAME}:${process.env.SITE_PORT}`;
const apiUrl = `${baseUrl}/api/v1`;
export const userUrl = `${apiUrl}/users`;
export const customerUrl = `${apiUrl}/customers`;
export const productUrl = `${apiUrl}/products`;
export const tokenUrl = `${apiUrl}/token`;
const updateTokenUrl = `${apiUrl}/token#update`
export const orderUrl = `${apiUrl}/orders`;
export const transportationUrl = `${apiUrl}/transportations`;
export const tokenHeader = 'SESSION-TOKEN';