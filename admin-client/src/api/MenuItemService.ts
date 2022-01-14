import RestService from "./RestService";
import { AxiosRequestConfig } from "axios";
import _merge from "lodash/merge";

export default class MenuItemService extends RestService {
  protected name = "menu-item";
  protected url = "/menu-items";

  constructor(protected menuId?: number | string) {
    super();
  }

  indexQueryKey() {
    return `${super.indexQueryKey()}-${this.menuId}`;
  }

  modelOptionsQueryKey() {
    return `${super.modelOptionsQueryKey()}-${this.menuId}`;
  }

  indexConfig(): AxiosRequestConfig {
    return _merge(super.indexConfig(), { params: { menu_id: this.menuId } });
  }

  createConfig(): AxiosRequestConfig {
    return _merge(super.createConfig(), { data: { menu_id: this.menuId } });
  }

  modelOptionsConfig(): AxiosRequestConfig {
    return _merge(super.modelOptionsConfig(), {
      params: { menu_id: this.menuId },
    });
  }
}

export const menuItemService = new MenuItemService();
