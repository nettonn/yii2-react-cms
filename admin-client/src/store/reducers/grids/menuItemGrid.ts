import createGridSlice from "./createGridSlice";

export const menuItemGridSlice = createGridSlice("menuItemGrid");

export const menuItemGridReducer = menuItemGridSlice.reducer;

export const menuItemGridActions = menuItemGridSlice.actions;
