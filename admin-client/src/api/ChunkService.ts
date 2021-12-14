import RestService from "./RestService";

export default class ChunkService extends RestService {
  protected name = "chunk";
  protected url = "/chunks";
}

export const chunkService = new ChunkService();
