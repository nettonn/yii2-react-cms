import RestService from "./RestService";

export default class ChunkService extends RestService {
  name = "chunk";
  url = "/chunks";
}

export const chunkService = new ChunkService();
