/**
 *
 * @property id
 * @property customerId
 * @property productId
 * @property {string} address
 * @property {Date|null} date
 * @property {string|null} agreementCode
 * @property {string|null} agreementUrl
 *
 */
export default class Order {
  /**
   *
   * @param props
   * @param props.id
   * @param props.customerId
   * @param props.productId
   * @param {string} props.address
   * @param {Date|string|null} [props.date]
   * @param {string|null} [props.agreementCode]
   * @param {string|null} [props.agreementUrl]
   *
   */
  constructor(props) {
    Object.assign(this, {date: null, agreementCode: null, agreementUrl: null});
    Object.assign(this, props);
    if(typeof this.date === 'string') {
      this.date = new Date(this.date);
    }
  }
}