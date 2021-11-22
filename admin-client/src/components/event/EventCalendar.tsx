import React, { FC } from "react";
import { Calendar } from "antd";
import { IEvent } from "../../models/IEvent";
import { Moment } from "moment";

interface EventCalendarProps {
  events: IEvent[];
}

const EventCalendar: FC<EventCalendarProps> = ({ events }) => {
  const dateCellRender = (value: Moment) => {
    const date = value.format("DD.MM.YYYY");
    const dayEvents = events.filter((event) => event.date === date);
    return dayEvents.map((event, index) => (
      <div key={index}>{event.description}</div>
    ));
  };

  const onSelect = (value: Moment) => {
    const date = value.format("DD.MM.YYYY");
    const dayEvents = events.filter((event) => event.date === date);
    console.log(dayEvents);
  };

  return (
    <Calendar
      dateCellRender={dateCellRender}
      onSelect={onSelect}
      mode={"month"}
    />
  );
};

export default EventCalendar;
