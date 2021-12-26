/**
 *
 * @property id
 * @property {string} role
 * @property {string} login
 *
 */
export default class User {
  /**
   *
   * @param props
   * @param props.id
   * @param {string} props.role
   * @param {string} props.login
   *
   */
  constructor(props) {
    Object.assign(this, props);
  }
}