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
   * @param {Date|string} props.plannedDate
   * @param {Date|string|null} [props.realDate]
   * @param {number} props.number
   * @param {string} props.status
   *
   */
  constructor(props) {
    Object.assign(this, {realDate: null});
    Object.assign(this, props);
    if(typeof this.plannedDate === 'string') {
      this.plannedDate = new Date(this.plannedDate);
    }
    if(typeof this.realDate === 'string') {
      this.realDate = new Date(this.realDate);
    }
  }
}