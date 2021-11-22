import RestService from "./RestService";

export default class PostService extends RestService {
  name = "post";
  url = "/posts";
}

export const postService = new PostService();
