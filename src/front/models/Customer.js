/**
 *
 * @property id
 * @property {string} name
 * @property {string|null} address
 * @property {string|null} phoneNumber
 *
 */
export default class Customer {
  /**
   *
   * @param props
   * @param props.id
   * @param {string} props.name
   * @param {string|null} props.address
   * @param {string|null} props.phoneNumber
   *
   */
  constructor(props) {
    Object.assign(this, props);
  }
}