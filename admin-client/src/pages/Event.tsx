import EventCalendar from "../components/event/EventCalendar";
import EventForm from "../components/event/EventForm";
import PageActions from "../components/crud/PageActions/PageActions";
import PageHeader from "../components/ui/PageHeader/PageHeader";
import { RouteNames } from "../routes";
import { Button, Form, Modal, message, Spin } from "antd";
import React, { FC, useEffect, useState } from "react";
import { useAppActions, useAppSelector } from "../hooks/redux";
import { IEvent } from "../models/IEvent";
import { eventActions } from "../store/reducers/event";

const Event: FC = () => {
  const [isLoading, setIsLoading] = useState(false);
  const [isInit, setIsInit] = useState(false);
  const [isModalVisible, setIsModalVisible] = useState(false);
  const [form] = Form.useForm();

  const { fetchGuests, fetchEvents, createEvent } = useAppActions(eventActions);
  const { identity } = useAppSelector((state) => state.auth);
  const { guests, error, events } = useAppSelector((state) => state.event);

  const onSubmit = async (values: any) => {
    setIsLoading(true);
    await createEvent({
      author: identity.id,
      description: values.description,
      date: values.date.format("DD.MM.YYYY"),
      guests: values.guests,
    } as IEvent);
    form.resetFields();
    setIsModalVisible(false);
    await fetchEvents(identity.id);
    setIsLoading(false);
    message.success("Событие добавлено!");
  };

  useEffect(() => {
    if (error) message.error(error);
  }, [error]);

  useEffect(() => {
    const fetch = async () => {
      await fetchGuests();
      await fetchEvents(identity.id);
    };
    setIsLoading(true);
    fetch().then(() => {
      setIsLoading(false);
      setIsInit(true);
    });
  }, [fetchGuests, fetchEvents, identity.id]);

  return (
    <>
      <PageHeader title="События" backPath={RouteNames.home} />
      <Spin spinning={isLoading}>
        <EventCalendar events={isInit ? events : []} />
      </Spin>
      <Modal
        title="Дабавление события"
        visible={isModalVisible}
        onOk={form.submit}
        onCancel={() => setIsModalVisible(false)}
        okButtonProps={{ loading: isLoading }}
      >
        <EventForm guests={guests} form={form} onSubmit={onSubmit} />
      </Modal>
      <PageActions
        content={
          <Button type="primary" onClick={() => setIsModalVisible(true)}>
            Добавить событие
          </Button>
        }
      />
    </>
  );
};

export default Event;
