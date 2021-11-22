import RestService from "./RestService";

export default class UserService extends RestService {
  name = "user";
  url = "/users";
}

export const userService = new UserService();
