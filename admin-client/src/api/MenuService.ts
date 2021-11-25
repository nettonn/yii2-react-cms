import RestService from "./RestService";

export default class MenuService extends RestService {
  name = "menu";
  url = "/menu";
}
export const menuService = new MenuService();
