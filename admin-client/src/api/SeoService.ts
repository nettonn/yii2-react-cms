import RestService from "./RestService";

export default class SeoService extends RestService {
  name = "seo";
  url = "/seo";
}
export const seoService = new SeoService();
