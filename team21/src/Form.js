import {
    Button,
    Cascader,
    DatePicker,
    Form,
    Input,
    InputNumber,
    Radio,
    Select,
    Switch,
    TreeSelect,
  } from 'antd';
  import React, { useState } from 'react';
  
  const SignUpForm = () => {
    const [componentSize, setComponentSize] = useState('default');
  
    const onFormLayoutChange = ({ size }) => {
      setComponentSize(size);
    };
  
    return (
      <Form
        labelCol={{
          span: 4,
        }}
        wrapperCol={{
          span: 14,
        }}
        layout="horizontal"
        initialValues={{
          size: componentSize,
        }}
        onValuesChange={onFormLayoutChange}
        size={componentSize}
      >
        <Form.Item label="Form Size" name="size">
          <Radio.Group>
            <Radio.Button value="small">Small</Radio.Button>
            <Radio.Button value="default">Default</Radio.Button>
            <Radio.Button value="large">Large</Radio.Button>
          </Radio.Group>
        </Form.Item>

        <Form.Item label="Role">
        <Select>
            <Select.Option value="Student">Student</Select.Option>
            <Select.Option value="Tutor">Tutor</Select.Option>
          </Select>
        </Form.Item>

        <Form.Item label="Level">
        <Select>
            <Select.Option value="K-12">K-12</Select.Option>
            <Select.Option value="University">University</Select.Option>
          </Select>
        </Form.Item>

        <Form.Item label="Name">
          <Input />
        </Form.Item>
        <Form.Item label="Age">
          <InputNumber />
        </Form.Item>
        <Form.Item label="Subject">
          <Cascader
            options={[
              {
                value: 'Math',
                label: 'Math',
                children: [
                  {
                    value: 'Calculus 12',
                    label: 'Calculus 12',
                  },
                  {
                    value: 'Trigonometry',
                    label: 'Trigonometry',
                  },
                  {
                    value: 'Pre-calculus',
                    label: 'Pre-calculus',
                  },
                ],
            },
            {
                value: 'Physics',
                label: 'Physics',
                children: [
                  {
                    value: 'Thermodynamics',
                    label: 'Thermodynamics',
                  },
                  {
                    value: 'Electromagnetism',
                    label: 'Electromagnetism',
                  },
                ],
            },
            {
                value: 'English',
                label: 'English',
                children: [
                  {
                    value: 'Essay Writing',
                    label: 'Essay Writing',
                  },
                ],
            },
              
            ]}
          />
        </Form.Item>
        <Form.Item label="DatePicker">
          <DatePicker />
        </Form.Item>
        <Form.Item label="Switch" valuePropName="checked">
          <Switch />
        </Form.Item>
        <Form.Item label="Button">
          <Button>Button</Button>
        </Form.Item>
      </Form>
    );
  };
  
  export default SignUpForm;