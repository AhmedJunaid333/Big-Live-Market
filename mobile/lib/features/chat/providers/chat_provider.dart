import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../main.dart';
import '../services/chat_api_service.dart';

final chatApiServiceProvider = Provider<ChatApiService>((ref) {
  final client = ref.watch(apiClientProvider);
  return ChatApiService(client);
});

final conversationsProvider = FutureProvider.autoDispose<List<dynamic>>((ref) async {
  final service = ref.watch(chatApiServiceProvider);
  return service.getConversations();
});

final conversationMessagesProvider = FutureProvider.family.autoDispose<List<dynamic>, int>((ref, conversationId) async {
  final service = ref.watch(chatApiServiceProvider);
  return service.getMessages(conversationId);
});
