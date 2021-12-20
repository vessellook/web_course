export default class Product {
  /**
   *
   * @param props
   * @param props.id
   * @param {string} props.name
   * @param {string} props.uid
   * @param {number} props.price
   * @param {number} props.count
   *
   */
  constructor(props) {
    Object.assign(this, props);
  }
}