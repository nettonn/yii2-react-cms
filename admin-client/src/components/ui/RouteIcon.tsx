import React, { FC } from "react";
import { routeIcons } from "../../routes";

interface RouteIconProps {
  route: keyof typeof routeIcons;
}

const RouteIcon: FC<RouteIconProps> = ({ route }) => {
  if (routeIcons[route]) {
    return React.createElement(routeIcons[route]);
  }
  return null;
};

export default RouteIcon;
