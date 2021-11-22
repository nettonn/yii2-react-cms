import { createAsyncThunk } from "@reduxjs/toolkit";
import { authService } from "../../../api/AuthService";
import { IIdentity } from "../../../models/IIdentity";
import { requestErrorHandler } from "../../../utils/functions";
import { IValidationErrorType } from "../../../types";

export const authActionCreators = {
  login: createAsyncThunk<
    {
      identity: IIdentity;
      token: string;
    },
    { email: string; password: string },
    {
      rejectValue: IValidationErrorType[];
    }
  >("auth/login", async ({ email, password }, { rejectWithValue }) => {
    try {
      const response = await authService.login(email, password);
      return response.data;
    } catch (e: any) {
      const errors = requestErrorHandler(e);
      if (errors.validationErrors) {
        return rejectWithValue(errors.validationErrors);
      }
      throw new Error(errors.message);
    }
  }),
  logout: createAsyncThunk("auth/logout", async (_, thunkApi) => {
    try {
      await authService.logout();
    } catch (e: any) {
      const errors = requestErrorHandler(e);
      throw new Error(errors.message);
    }
  }),
};
