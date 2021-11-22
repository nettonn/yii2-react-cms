import RestService from "./RestService";

export default class PageService extends RestService {
  name = "page";
  url = "/pages";
}
export const pageService = new PageService();
