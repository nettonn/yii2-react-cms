import RestService from "./RestService";

export default class SeoService extends RestService {
  protected name = "seo";
  protected url = "/seo";
}
export const seoService = new SeoService();
