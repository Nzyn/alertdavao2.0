import React from 'react';
import { Modal, View, Text, TouchableOpacity, StyleSheet } from 'react-native';

interface Props {
  visible: boolean;
  title?: string;
  message: string;
  okText?: string;
  onOk: () => void;
}

export default function UpdateSuccessDialog({
  visible,
  title = 'Success',
  message,
  okText = 'OK',
  onOk,
}: Props) {
  return (
    <Modal visible={visible} transparent animationType="fade">
      <View style={styles.overlay}>
        <View style={styles.card}>
          <Text style={styles.title}>{title}</Text>
          <Text style={styles.message}>{message}</Text>
          <View style={styles.actions}>
            <TouchableOpacity style={styles.okButton} onPress={onOk} activeOpacity={0.8}>
              <Text style={styles.okText}>{okText}</Text>
            </TouchableOpacity>
          </View>
        </View>
      </View>
    </Modal>
  );
}

const styles = StyleSheet.create({
  overlay: {
    flex: 1,
    backgroundColor: 'rgba(0,0,0,0.5)',
    alignItems: 'center',
    justifyContent: 'center',
    paddingHorizontal: 24,
  },
  card: {
    width: '100%',
    backgroundColor: '#fff',
    borderRadius: 12,
    padding: 20,
  },
  title: {
    fontSize: 18,
    fontWeight: '700',
    textAlign: 'center',
    marginBottom: 8,
    color: '#222',
  },
  message: {
    fontSize: 14,
    color: '#444',
    textAlign: 'center',
    marginVertical: 12,
  },
  actions: {
    alignItems: 'center',
    marginTop: 4,
  },
  okButton: {
    minWidth: 120,
    backgroundColor: '#1D3557',
    borderRadius: 8,
    paddingVertical: 10,
    paddingHorizontal: 16,
  },
  okText: {
    color: '#fff',
    fontSize: 16,
    fontWeight: '600',
    textAlign: 'center',
  },
});
