import RestService from "./RestService";

export default class OrderService extends RestService {
  protected name = "order";
  protected url = "/orders";
}

export const orderService = new OrderService();
