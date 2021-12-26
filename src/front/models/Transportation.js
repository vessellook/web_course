/**
 *
 * @property id
 * @property orderId
 * @property {Date} plannedDate
 * @property {Date|null} realDate
 * @property {number} number
 * @property {string} status
 *
 */
export default class Transportation {
  /**
   *
   * @param props
   * @param props.id
   * @param props.orderId
   * @param {Date} props.plannedDate
   * @param {Date|null} props.realDate
   * @param {number} props.number
   * @param {string} props.status
   *
   */
  constructor(props) {
    Object.assign(this, props);
  }
}