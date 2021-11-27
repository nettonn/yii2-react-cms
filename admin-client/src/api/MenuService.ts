import RestService from "./RestService";

export default class MenuService extends RestService {
  protected name = "menu";
  protected url = "/menu";
}
export const menuService = new MenuService();
