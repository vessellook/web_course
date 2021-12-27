import Customers from "@/views/Customers";
import Orders from "@/views/Orders";
import Transportations from "@/views/Transportations";
import Users from "@/views/Users";
import Profile from "@/views/Profile";
import Home from "@/views/Home";
import CreateOrder from "@/views/CreateOrder";

export default [
  {
    path: '/',
    name: 'Home',
    component: Home
  },
  {
    path: '/customers/:id?',
    props: true,
    name: 'CustomerList',
    component: Customers
  },
  {
    path: '/orders/:id?',
    props: true,
    name: 'OrderList',
    component: Orders
  },
  {
    path: '/orders/new',
    name: 'NewOrder',
    props: true,
    component: CreateOrder
  },
  {
    path: '/transportations/:id?',
    props: true,
    name: 'TransportationList',
    component: Transportations
  },
  {
    path: '/users/:id?',
    props: true,
    name: 'UserList',
    component: Users
  },
  {
    path: '/settings',
    props: true,
    name: 'Profile',
    component: Profile
  }
];