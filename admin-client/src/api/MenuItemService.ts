import RestService from "./RestService";
import { AxiosRequestConfig } from "axios";

export default class MenuItemService extends RestService {
  name = "menu-item";
  url = "/menu-items";
  menuId?: number | string;

  indexQueryKey() {
    return `${this.name}-index-${this.menuId}`;
  }

  modelOptionsQueryKey() {
    return `${this.name}-model-options-${this.menuId}`;
  }

  indexConfig(): AxiosRequestConfig {
    return {
      url: this.url,
      method: "get",
      params: {
        menuId: this.menuId,
      },
    };
  }

  modelOptionsConfig(): AxiosRequestConfig {
    return {
      url: `${this.url}/model-options`,
      method: "get",
      params: {
        menuId: this.menuId,
      },
    };
  }
}
export const menuItemService = new MenuItemService();
