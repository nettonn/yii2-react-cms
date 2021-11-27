import RestService from "./RestService";

export default class SettingService extends RestService {
  protected name = "setting";
  protected url = "/settings";
}

export const settingService = new SettingService();
