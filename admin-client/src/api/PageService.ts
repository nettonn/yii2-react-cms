import RestService from "./RestService";

export default class PageService extends RestService {
  protected name = "page";
  protected url = "/pages";
}
export const pageService = new PageService();
