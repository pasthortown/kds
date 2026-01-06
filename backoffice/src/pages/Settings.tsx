import { useEffect, useState } from 'react';
import {
  Card,
  Form,
  Input,
  Button,
  message,
  Tabs,
  Space,
  Tag,
  Descriptions,
  Alert,
  Divider,
  Row,
  Col,
  Radio,
  Typography,
  Table,
} from 'antd';
import {
  SaveOutlined,
  ReloadOutlined,
  SyncOutlined,
  CheckCircleOutlined,
  CloseCircleOutlined,
  ApiOutlined,
  PrinterOutlined,
  CloudServerOutlined,
  DesktopOutlined,
  FileTextOutlined,
} from '@ant-design/icons';
import { configApi } from '../services/api';

const { Text } = Typography;
const { TextArea } = Input;

interface HealthStatus {
  database: boolean;
  redis: boolean;
  websocket: boolean;
}

interface ScreenPrinter {
  id: string;
  name: string;
  number: number;
  printerName: string;
}

interface ConfigModes {
  ticketMode: 'POLLING' | 'API';
  printMode: 'LOCAL' | 'CENTRALIZED';
  centralizedPrintUrl: string;
  centralizedPrintUrlBackup: string;
  centralizedPrintPort: number;
  printTemplate: string;
  printTemplateType: string;
  screenPrinters: ScreenPrinter[];
}

export function Settings() {
  const [loading, setLoading] = useState(true);
  const [healthStatus, setHealthStatus] = useState<HealthStatus | null>(null);
  const [configModes, setConfigModes] = useState<ConfigModes | null>(null);
  const [screenPrinters, setScreenPrinters] = useState<ScreenPrinter[]>([]);
  const [testingCentralized, setTestingCentralized] = useState(false);
  const [modesForm] = Form.useForm();

  useEffect(() => {
    loadConfig();
  }, []);

  const loadConfig = async () => {
    try {
      setLoading(true);
      const [healthRes, modesRes] = await Promise.all([
        configApi.health(),
        configApi.getModes(),
      ]);

      // El health devuelve { status, checks: { database, redis, websocket }, timestamp }
      setHealthStatus(healthRes.data.checks || healthRes.data);
      setConfigModes(modesRes.data);
      setScreenPrinters(modesRes.data.screenPrinters || []);

      modesForm.setFieldsValue(modesRes.data);
    } catch (error) {
      message.error('Error cargando configuracion');
    } finally {
      setLoading(false);
    }
  };

  const handleSaveModes = async () => {
    try {
      const values = await modesForm.validateFields();
      // Incluir screenPrinters en el guardado
      await configApi.updateModes({ ...values, screenPrinters });
      message.success('Modos de configuracion guardados');
      loadConfig();
    } catch (error) {
      message.error('Error guardando modos de configuracion');
    }
  };

  const handlePrinterChange = (screenId: string, printerName: string) => {
    setScreenPrinters(prev =>
      prev.map(sp =>
        sp.id === screenId ? { ...sp, printerName } : sp
      )
    );
  };

  const handleTestCentralizedPrint = async () => {
    try {
      setTestingCentralized(true);
      const { data } = await configApi.testCentralizedPrint();
      if (data.success) {
        message.success(`Conexion exitosa: ${data.message}`);
      } else {
        message.error(`Error de conexion: ${data.message}`);
      }
    } catch (error) {
      message.error('Error probando conexion');
    } finally {
      setTestingCentralized(false);
    }
  };

  const getHealthIcon = (status: boolean) =>
    status ? (
      <CheckCircleOutlined style={{ color: '#52c41a', fontSize: 20 }} />
    ) : (
      <CloseCircleOutlined style={{ color: '#ff4d4f', fontSize: 20 }} />
    );

  return (
    <div>
      <Tabs
        items={[
          {
            key: 'modes',
            label: 'Modos',
            children: (
              <Card
                title="Modos de Configuracion"
                extra={
                  <Button
                    type="primary"
                    icon={<SaveOutlined />}
                    onClick={handleSaveModes}
                  >
                    Guardar
                  </Button>
                }
                loading={loading}
              >
                <Form form={modesForm} layout="vertical" style={{ maxWidth: 800 }}>
                  <Alert
                    type="info"
                    message="Configuracion de Modos"
                    description="Seleccione como el sistema obtendra los tickets y como realizara la impresion."
                    style={{ marginBottom: 24 }}
                  />

                  <Divider orientation="left">
                    <ApiOutlined /> Modo de Tickets
                  </Divider>

                  <Form.Item
                    name="ticketMode"
                    label="Origen de Tickets"
                    extra="Define como el sistema obtiene las comandas/ordenes."
                  >
                    <Radio.Group>
                      <Space direction="vertical">
                        <Radio value="POLLING">
                          <Space>
                            <DesktopOutlined />
                            <Text strong>POLLING (MAXPOINT)</Text>
                          </Space>
                          <br />
                          <Text type="secondary" style={{ marginLeft: 24 }}>
                            El sistema consulta periodicamente la base de datos MAXPOINT para obtener nuevas ordenes.
                          </Text>
                        </Radio>
                        <Radio value="API">
                          <Space>
                            <ApiOutlined />
                            <Text strong>API (Recepcion via HTTP)</Text>
                          </Space>
                          <br />
                          <Text type="secondary" style={{ marginLeft: 24 }}>
                            Las ordenes son enviadas al sistema via API REST. Compatible con integraciones externas.
                          </Text>
                        </Radio>
                      </Space>
                    </Radio.Group>
                  </Form.Item>

                  {configModes?.ticketMode === 'API' && (
                    <Alert
                      type="warning"
                      message="Modo API Activo"
                      description={
                        <div>
                          <p>El polling desde MAXPOINT esta deshabilitado. Las ordenes deben enviarse via:</p>
                          <ul>
                            <li><code>POST /api/tickets/receive</code> - Una orden individual</li>
                            <li><code>POST /api/tickets/receive-batch</code> - Multiples ordenes</li>
                            <li><code>POST /api/comandas</code> - Compatible con sistema anterior</li>
                          </ul>
                        </div>
                      }
                      style={{ marginBottom: 16 }}
                    />
                  )}

                  <Divider orientation="left">
                    <PrinterOutlined /> Modo de Impresion
                  </Divider>

                  <Form.Item
                    name="printMode"
                    label="Metodo de Impresion"
                    extra="Define como el sistema enviara los tickets a las impresoras."
                  >
                    <Radio.Group>
                      <Space direction="vertical">
                        <Radio value="LOCAL">
                          <Space>
                            <PrinterOutlined />
                            <Text strong>LOCAL (TCP Directo)</Text>
                          </Space>
                          <br />
                          <Text type="secondary" style={{ marginLeft: 24 }}>
                            El backend envia directamente a la impresora via TCP/IP (ESC/POS).
                          </Text>
                        </Radio>
                        <Radio value="CENTRALIZED">
                          <Space>
                            <CloudServerOutlined />
                            <Text strong>CENTRALIZADO (Servicio HTTP)</Text>
                          </Space>
                          <br />
                          <Text type="secondary" style={{ marginLeft: 24 }}>
                            Las ordenes se envian a un servicio centralizado de impresion via HTTP.
                          </Text>
                        </Radio>
                      </Space>
                    </Radio.Group>
                  </Form.Item>

                  <Form.Item noStyle shouldUpdate={(prev, curr) => prev.printMode !== curr.printMode}>
                    {({ getFieldValue }) =>
                      getFieldValue('printMode') === 'CENTRALIZED' && (
                        <Card size="small" style={{ marginBottom: 16, backgroundColor: '#fafafa' }}>
                          <Form.Item
                            name="centralizedPrintUrl"
                            label="URL del Servicio Principal"
                            rules={[{ required: true, message: 'Ingrese la URL del servicio' }]}
                          >
                            <Input
                              placeholder="http://192.168.1.100:5000/api/ImpresionTickets/Impresion"
                              addonBefore={<CloudServerOutlined />}
                            />
                          </Form.Item>

                          <Form.Item
                            name="centralizedPrintUrlBackup"
                            label="URL del Servicio Backup"
                            extra="Si el servicio principal no responde, se usara esta URL de respaldo"
                          >
                            <Input
                              placeholder="http://192.168.1.101:5000/api/ImpresionTickets/Impresion"
                              addonBefore={<CloudServerOutlined />}
                            />
                          </Form.Item>

                          <Form.Item
                            name="printTemplateType"
                            label="Tipo de Plantilla"
                            extra="Identificador del tipo de plantilla (ej: orden_pedido)"
                          >
                            <Input
                              placeholder="orden_pedido"
                              addonBefore={<FileTextOutlined />}
                            />
                          </Form.Item>

                          <Divider orientation="left" style={{ marginTop: 8 }}>
                            <FileTextOutlined /> Plantilla XML
                          </Divider>

                          <Form.Item
                            name="printTemplate"
                            label="PlantillaXML"
                            extra="Plantilla XML para el formato de impresion. Debe contener la estructura completa del ticket."
                          >
                            <TextArea
                              rows={8}
                              placeholder='<?xml version="1.0" encoding="utf-8"?><plantilla id="impresionOrdenPedidoLocal">...</plantilla>'
                              style={{ fontFamily: 'monospace', fontSize: 12 }}
                            />
                          </Form.Item>

                          <Divider orientation="left" style={{ marginTop: 16 }}>
                            <PrinterOutlined /> Impresoras por Pantalla
                          </Divider>

                          <Table
                            dataSource={screenPrinters}
                            rowKey="id"
                            size="small"
                            pagination={false}
                            columns={[
                              {
                                title: '#',
                                dataIndex: 'number',
                                width: 50,
                                align: 'center',
                              },
                              {
                                title: 'Pantalla',
                                dataIndex: 'name',
                                width: 200,
                              },
                              {
                                title: 'Impresora',
                                dataIndex: 'printerName',
                                render: (value: string, record: ScreenPrinter) => (
                                  <Input
                                    value={value}
                                    onChange={(e) => handlePrinterChange(record.id, e.target.value)}
                                    placeholder="Nombre de la impresora (ej: linea)"
                                    size="small"
                                  />
                                ),
                              },
                            ]}
                            locale={{ emptyText: 'No hay pantallas configuradas' }}
                          />
                          <Text type="secondary" style={{ fontSize: 12, marginTop: 8, display: 'block' }}>
                            Configure el nombre de la impresora que usara cada pantalla para la impresion centralizada.
                          </Text>

                          <Divider style={{ marginTop: 16 }} />

                          <Button
                            onClick={handleTestCentralizedPrint}
                            loading={testingCentralized}
                            icon={<SyncOutlined />}
                            type="default"
                          >
                            Probar Conexion
                          </Button>
                        </Card>
                      )
                    }
                  </Form.Item>

                  <Divider />

                  <Alert
                    type="success"
                    message="Estado Actual"
                    description={
                      <Descriptions column={2} size="small">
                        <Descriptions.Item label="Modo de Tickets">
                          <Tag color={configModes?.ticketMode === 'POLLING' ? 'blue' : 'green'}>
                            {configModes?.ticketMode || 'POLLING'}
                          </Tag>
                        </Descriptions.Item>
                        <Descriptions.Item label="Modo de Impresion">
                          <Tag color={configModes?.printMode === 'LOCAL' ? 'blue' : 'purple'}>
                            {configModes?.printMode || 'LOCAL'}
                          </Tag>
                        </Descriptions.Item>
                      </Descriptions>
                    }
                  />
                </Form>
              </Card>
            ),
          },
          {
            key: 'health',
            label: 'Estado del Sistema',
            children: (
              <Card
                title="Estado de Servicios"
                extra={
                  <Button icon={<ReloadOutlined />} onClick={loadConfig}>
                    Actualizar
                  </Button>
                }
              >
                <Row gutter={[16, 16]}>
                  <Col span={8}>
                    <Card>
                      <Space direction="vertical" align="center" style={{ width: '100%' }}>
                        {getHealthIcon(healthStatus?.database || false)}
                        <span>Base de Datos</span>
                        <Tag color={healthStatus?.database ? 'green' : 'red'}>
                          {healthStatus?.database ? 'Conectado' : 'Desconectado'}
                        </Tag>
                      </Space>
                    </Card>
                  </Col>
                  <Col span={8}>
                    <Card>
                      <Space direction="vertical" align="center" style={{ width: '100%' }}>
                        {getHealthIcon(healthStatus?.redis || false)}
                        <span>Redis</span>
                        <Tag color={healthStatus?.redis ? 'green' : 'red'}>
                          {healthStatus?.redis ? 'Conectado' : 'Desconectado'}
                        </Tag>
                      </Space>
                    </Card>
                  </Col>
                  <Col span={8}>
                    <Card>
                      <Space direction="vertical" align="center" style={{ width: '100%' }}>
                        {getHealthIcon(healthStatus?.websocket || false)}
                        <span>WebSocket</span>
                        <Tag color={healthStatus?.websocket ? 'green' : 'red'}>
                          {healthStatus?.websocket ? 'Activo' : 'Inactivo'}
                        </Tag>
                      </Space>
                    </Card>
                  </Col>
                </Row>

                <Divider />

                <Alert
                  type="info"
                  message="Informacion del Sistema"
                  description={
                    <Descriptions column={2} size="small">
                      <Descriptions.Item label="Version">2.0.0</Descriptions.Item>
                      <Descriptions.Item label="Node.js">v20.x</Descriptions.Item>
                      <Descriptions.Item label="Uptime">
                        {new Date().toLocaleString()}
                      </Descriptions.Item>
                      <Descriptions.Item label="Ambiente">
                        {import.meta.env.MODE}
                      </Descriptions.Item>
                    </Descriptions>
                  }
                />
              </Card>
            ),
          },
        ]}
      />
    </div>
  );
}
