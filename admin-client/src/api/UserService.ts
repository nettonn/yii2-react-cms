import RestService from "./RestService";

export default class UserService extends RestService {
  protected name = "user";
  protected url = "/users";
}

export const userService = new UserService();
