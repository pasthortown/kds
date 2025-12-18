import { useEffect, useState } from 'react';
import {
  Card,
  Table,
  Button,
  Modal,
  Form,
  Input,
  Select,
  Tag,
  Space,
  message,
  Popconfirm,
  Collapse,
  List,
  InputNumber,
  Statistic,
  Row,
  Col,
} from 'antd';
import {
  PlusOutlined,
  EditOutlined,
  DeleteOutlined,
  ReloadOutlined,
  FilterOutlined,
  ApiOutlined,
  SyncOutlined,
} from '@ant-design/icons';
import { queuesApi } from '../services/api';

interface Channel {
  id: string;
  channel: string;
  color: string;
  priority: number;
  active: boolean;
}

interface Filter {
  id: string;
  pattern: string;
  suppress: boolean;
  active: boolean;
}

interface ScreenBasic {
  id: string;
  name: string;
  status: string;
}

interface QueueStats {
  pending: number;
  inProgress: number;
  completed: number;
  avgTime: number;
}

interface Queue {
  id: string;
  name: string;
  description: string | null;
  distribution: 'DISTRIBUTED' | 'SINGLE';
  active: boolean;
  channels: Channel[];
  filters: Filter[];
  screens: ScreenBasic[];
  stats?: QueueStats;
}

export function Queues() {
  const [queues, setQueues] = useState<Queue[]>([]);
  const [loading, setLoading] = useState(true);
  const [modalOpen, setModalOpen] = useState(false);
  const [channelModalOpen, setChannelModalOpen] = useState(false);
  const [filterModalOpen, setFilterModalOpen] = useState(false);
  const [editingQueue, setEditingQueue] = useState<Queue | null>(null);
  const [selectedQueue, setSelectedQueue] = useState<Queue | null>(null);
  const [form] = Form.useForm();
  const [channelForm] = Form.useForm();
  const [filterForm] = Form.useForm();

  useEffect(() => {
    loadData();
  }, []);

  const loadData = async () => {
    try {
      setLoading(true);
      const { data } = await queuesApi.getAll();

      // Cargar stats de cada cola
      const queuesWithStats = await Promise.all(
        data.map(async (q: Queue) => {
          try {
            const statsRes = await queuesApi.getStats(q.id);
            return { ...q, stats: statsRes.data };
          } catch {
            return q;
          }
        })
      );

      setQueues(queuesWithStats);
    } catch (error) {
      message.error('Error cargando colas');
    } finally {
      setLoading(false);
    }
  };

  const handleCreate = () => {
    setEditingQueue(null);
    form.resetFields();
    form.setFieldsValue({ distribution: 'DISTRIBUTED' });
    setModalOpen(true);
  };

  const handleEdit = (queue: Queue) => {
    setEditingQueue(queue);
    form.setFieldsValue({
      name: queue.name,
      description: queue.description,
      distribution: queue.distribution,
    });
    setModalOpen(true);
  };

  const handleSubmit = async () => {
    try {
      const values = await form.validateFields();

      if (editingQueue) {
        await queuesApi.update(editingQueue.id, values);
        message.success('Cola actualizada');
      } else {
        await queuesApi.create(values);
        message.success('Cola creada');
      }

      setModalOpen(false);
      loadData();
    } catch (error: any) {
      message.error(error.response?.data?.error || 'Error guardando cola');
    }
  };

  const handleDelete = async (id: string) => {
    try {
      await queuesApi.delete(id);
      message.success('Cola eliminada');
      loadData();
    } catch (error) {
      message.error('Error eliminando cola');
    }
  };

  const handleAddChannel = (queue: Queue) => {
    setSelectedQueue(queue);
    channelForm.resetFields();
    channelForm.setFieldsValue({ type: 'MAXPOINT', enabled: true });
    setChannelModalOpen(true);
  };

  const handleSaveChannel = async () => {
    if (!selectedQueue) return;
    try {
      const values = await channelForm.validateFields();
      await queuesApi.addChannel(selectedQueue.id, values);
      message.success('Canal agregado');
      setChannelModalOpen(false);
      loadData();
    } catch (error) {
      message.error('Error agregando canal');
    }
  };

  const handleDeleteChannel = async (queueId: string, channelId: string) => {
    try {
      await queuesApi.deleteChannel(queueId, channelId);
      message.success('Canal eliminado');
      loadData();
    } catch (error) {
      message.error('Error eliminando canal');
    }
  };

  const handleAddFilter = (queue: Queue) => {
    setSelectedQueue(queue);
    filterForm.resetFields();
    setFilterModalOpen(true);
  };

  const handleSaveFilter = async () => {
    if (!selectedQueue) return;
    try {
      const values = await filterForm.validateFields();
      await queuesApi.addFilter(selectedQueue.id, values);
      message.success('Filtro agregado');
      setFilterModalOpen(false);
      loadData();
    } catch (error) {
      message.error('Error agregando filtro');
    }
  };

  const handleDeleteFilter = async (queueId: string, filterId: string) => {
    try {
      await queuesApi.deleteFilter(queueId, filterId);
      message.success('Filtro eliminado');
      loadData();
    } catch (error) {
      message.error('Error eliminando filtro');
    }
  };

  const handleResetBalance = async (queueId: string) => {
    try {
      await queuesApi.resetBalance(queueId);
      message.success('Balance reseteado');
    } catch (error) {
      message.error('Error reseteando balance');
    }
  };

  const columns = [
    {
      title: 'Nombre',
      dataIndex: 'name',
      key: 'name',
      width: 200,
      render: (name: string, record: Queue) => (
        <Space direction="vertical" size={0}>
          <span style={{ fontWeight: 500 }}>{name}</span>
          {record.description && (
            <span style={{ fontSize: 12, color: '#888' }}>{record.description}</span>
          )}
        </Space>
      ),
    },
    {
      title: 'Distribución',
      dataIndex: 'distribution',
      key: 'distribution',
      width: 120,
      render: (mode: string) => {
        const colors: Record<string, string> = {
          DISTRIBUTED: 'blue',
          SINGLE: 'orange',
        };
        const labels: Record<string, string> = {
          DISTRIBUTED: 'Distribuida',
          SINGLE: 'Única',
        };
        return <Tag color={colors[mode]}>{labels[mode] || mode}</Tag>;
      },
    },
    {
      title: 'Pantallas',
      key: 'screensCount',
      width: 90,
      align: 'center' as const,
      render: (_: any, record: Queue) => <Tag>{record.screens?.length || 0}</Tag>,
    },
    {
      title: 'Canales',
      key: 'channels',
      width: 100,
      render: (_: any, record: Queue) => (
        <Space size={2} wrap>
          {record.channels.length > 0 ? (
            record.channels.map((ch) => (
              <Tag key={ch.id} color={ch.active ? ch.color : 'default'} style={{ margin: 0 }}>
                {ch.channel}
              </Tag>
            ))
          ) : (
            <Tag>-</Tag>
          )}
        </Space>
      ),
    },
    {
      title: 'Pendientes',
      key: 'pending',
      width: 90,
      align: 'center' as const,
      render: (_: any, record: Queue) =>
        record.stats ? (
          <Tag color="gold">{record.stats.pending}</Tag>
        ) : (
          '-'
        ),
    },
    {
      title: 'Completadas',
      key: 'completed',
      width: 100,
      align: 'center' as const,
      render: (_: any, record: Queue) =>
        record.stats ? (
          <Tag color="green">{record.stats.completed}</Tag>
        ) : (
          '-'
        ),
    },
    {
      title: 'Acciones',
      key: 'actions',
      width: 120,
      render: (_: any, record: Queue) => (
        <Space size={4}>
          <Button icon={<EditOutlined />} size="small" onClick={() => handleEdit(record)} />
          <Button
            icon={<SyncOutlined />}
            size="small"
            onClick={() => handleResetBalance(record.id)}
            title="Resetear Balance"
          />
          <Popconfirm title="Eliminar cola?" onConfirm={() => handleDelete(record.id)}>
            <Button icon={<DeleteOutlined />} size="small" danger />
          </Popconfirm>
        </Space>
      ),
    },
  ];

  const expandedRowRender = (record: Queue) => (
    <div style={{ padding: '0 16px' }}>
      <Row gutter={16} style={{ marginBottom: 16 }}>
        <Col span={6}>
          <Statistic title="Pendientes" value={record.stats?.pending || 0} />
        </Col>
        <Col span={6}>
          <Statistic title="En Progreso" value={record.stats?.inProgress || 0} />
        </Col>
        <Col span={6}>
          <Statistic title="Completadas" value={record.stats?.completed || 0} />
        </Col>
        <Col span={6}>
          <Statistic
            title="Tiempo Promedio"
            value={record.stats?.avgTime || 0}
            suffix="seg"
          />
        </Col>
      </Row>

      <Collapse
        items={[
          {
            key: 'channels',
            label: (
              <Space>
                <ApiOutlined />
                Canales ({record.channels.length})
              </Space>
            ),
            extra: (
              <Button
                size="small"
                icon={<PlusOutlined />}
                onClick={(e) => {
                  e.stopPropagation();
                  handleAddChannel(record);
                }}
              >
                Agregar
              </Button>
            ),
            children: (
              <List
                dataSource={record.channels}
                renderItem={(channel) => (
                  <List.Item
                    actions={[
                      <Popconfirm
                        key="delete"
                        title="Eliminar canal?"
                        onConfirm={() => handleDeleteChannel(record.id, channel.id)}
                      >
                        <Button size="small" danger icon={<DeleteOutlined />} />
                      </Popconfirm>,
                    ]}
                  >
                    <List.Item.Meta
                      title={
                        <Space>
                          {channel.channel}
                          <Tag color={channel.active ? channel.color : 'default'}>
                            Prioridad: {channel.priority}
                          </Tag>
                        </Space>
                      }
                    />
                  </List.Item>
                )}
                locale={{ emptyText: 'Sin canales configurados' }}
              />
            ),
          },
          {
            key: 'filters',
            label: (
              <Space>
                <FilterOutlined />
                Filtros ({record.filters.length})
              </Space>
            ),
            extra: (
              <Button
                size="small"
                icon={<PlusOutlined />}
                onClick={(e) => {
                  e.stopPropagation();
                  handleAddFilter(record);
                }}
              >
                Agregar
              </Button>
            ),
            children: (
              <List
                dataSource={record.filters}
                renderItem={(filter) => (
                  <List.Item
                    actions={[
                      <Popconfirm
                        key="delete"
                        title="Eliminar filtro?"
                        onConfirm={() => handleDeleteFilter(record.id, filter.id)}
                      >
                        <Button size="small" danger icon={<DeleteOutlined />} />
                      </Popconfirm>,
                    ]}
                  >
                    <List.Item.Meta
                      title={
                        <Space>
                          <span>{filter.pattern}</span>
                          <Tag color={filter.suppress ? 'red' : 'green'}>
                            {filter.suppress ? 'Ocultar' : 'Mostrar'}
                          </Tag>
                        </Space>
                      }
                    />
                  </List.Item>
                )}
                locale={{ emptyText: 'Sin filtros configurados' }}
              />
            ),
          },
        ]}
      />
    </div>
  );

  return (
    <div>
      <Card
        title="Gestion de Colas"
        extra={
          <Space>
            <Button icon={<ReloadOutlined />} onClick={loadData}>
              Actualizar
            </Button>
            <Button type="primary" icon={<PlusOutlined />} onClick={handleCreate}>
              Nueva Cola
            </Button>
          </Space>
        }
      >
        <Table
          dataSource={queues}
          columns={columns}
          rowKey="id"
          loading={loading}
          expandable={{ expandedRowRender }}
          pagination={false}
          size="middle"
        />
      </Card>

      {/* Modal Crear/Editar Cola */}
      <Modal
        title={editingQueue ? 'Editar Cola' : 'Nueva Cola'}
        open={modalOpen}
        onOk={handleSubmit}
        onCancel={() => setModalOpen(false)}
        okText="Guardar"
        cancelText="Cancelar"
      >
        <Form form={form} layout="vertical">
          <Form.Item
            name="name"
            label="Nombre"
            rules={[{ required: true, message: 'Ingrese el nombre' }]}
          >
            <Input placeholder="LINEAS" />
          </Form.Item>
          <Form.Item name="description" label="Descripcion">
            <Input.TextArea placeholder="Descripcion de la cola" rows={2} />
          </Form.Item>
          <Form.Item
            name="distribution"
            label="Tipo de Distribución"
            rules={[{ required: true }]}
          >
            <Select>
              <Select.Option value="DISTRIBUTED">
                Distribuida - Balanceado entre pantallas
              </Select.Option>
              <Select.Option value="SINGLE">
                Única - Una sola pantalla
              </Select.Option>
            </Select>
          </Form.Item>
        </Form>
      </Modal>

      {/* Modal Agregar Canal */}
      <Modal
        title="Agregar Canal"
        open={channelModalOpen}
        onOk={handleSaveChannel}
        onCancel={() => setChannelModalOpen(false)}
        okText="Agregar"
        cancelText="Cancelar"
      >
        <Form form={channelForm} layout="vertical">
          <Form.Item name="channel" label="Nombre del Canal" rules={[{ required: true }]}>
            <Input placeholder="Ej: KIOSKO, UBER, RAPPI" />
          </Form.Item>
          <Form.Item name="color" label="Color">
            <Input type="color" defaultValue="#4a90e2" style={{ width: 100, height: 32 }} />
          </Form.Item>
          <Form.Item name="priority" label="Prioridad">
            <InputNumber min={0} max={100} defaultValue={0} />
          </Form.Item>
        </Form>
      </Modal>

      {/* Modal Agregar Filtro */}
      <Modal
        title="Agregar Filtro"
        open={filterModalOpen}
        onOk={handleSaveFilter}
        onCancel={() => setFilterModalOpen(false)}
        okText="Agregar"
        cancelText="Cancelar"
      >
        <Form form={filterForm} layout="vertical">
          <Form.Item name="pattern" label="Patrón" rules={[{ required: true }]}>
            <Input placeholder="Ej: SANDUCHE, TWISTER, RUSTER" />
          </Form.Item>
          <Form.Item name="suppress" label="Acción" initialValue={false}>
            <Select>
              <Select.Option value={false}>Mostrar productos que coincidan</Select.Option>
              <Select.Option value={true}>Ocultar productos que coincidan</Select.Option>
            </Select>
          </Form.Item>
        </Form>
      </Modal>
    </div>
  );
}
