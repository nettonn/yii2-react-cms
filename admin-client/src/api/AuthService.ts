import { AxiosRequestConfig } from "axios";
import { $api, $apiNoAuth } from "../http/axios";
import { Identity } from "../models/Identity";
import { prepareAxiosConfig } from "../utils/functions";

export interface IAuthStorage {
  isAuth: boolean;
  token: string;
  identity: Identity;
}

const authStorageName = "auth";

export default class AuthService {
  protected loginUrl = "/auth/login";
  protected logoutUrl = "/auth/refresh-token";
  protected refreshTokenUrl = "/auth/refresh-token";

  async login(values: { email: string; password: string }): Promise<{
    identity: Identity;
    token: string;
  }> {
    const config = prepareAxiosConfig(this.loginConfig(), { data: values });
    const response = await $apiNoAuth.request(config);
    return response.data;
  }

  async logout(): Promise<null> {
    const config = prepareAxiosConfig(this.logoutConfig());
    const response = await $api.request(config);
    return response.data;
  }

  async refresh(): Promise<{}> {
    const config = prepareAxiosConfig(this.refreshConfig());
    const response = await $api.request(config);
    return response.data;
  }

  loginConfig(): AxiosRequestConfig {
    return {
      url: this.loginUrl,
      method: "post",
    };
  }

  logoutConfig(): AxiosRequestConfig {
    return {
      url: this.logoutUrl,
      method: "delete",
    };
  }

  refreshConfig(): AxiosRequestConfig {
    return {
      url: this.refreshTokenUrl,
      method: "post",
    };
  }

  setStorage(data: IAuthStorage) {
    localStorage.setItem(authStorageName, JSON.stringify(data));
  }

  getStorage(): IAuthStorage {
    const dataString = localStorage.getItem(authStorageName);
    if (!dataString) return {} as IAuthStorage;

    const data = JSON.parse(dataString);

    if (!this.isStorageValid(data)) {
      this.setStorage({} as IAuthStorage);
      return {} as IAuthStorage;
    }

    return data;
  }

  clearStorage() {
    localStorage.removeItem(authStorageName);
  }

  isStorageValid(data: IAuthStorage) {
    return typeof data === "object" && !Array.isArray(data) && data !== null;
  }

  setStorageToken(token: string) {
    this.setStorage({ ...this.getStorage(), token });
  }

  getStorageToken(): string {
    return this.getStorage().token;
  }
}

export const authService = new AuthService();
