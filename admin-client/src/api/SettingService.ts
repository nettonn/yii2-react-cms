import RestService from "./RestService";

export default class SettingService extends RestService {
  name = "setting";
  url = "/settings";
}

export const settingService = new SettingService();
