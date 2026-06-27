<?php

declare(strict_types=1);

namespace VZaps\Sdk\Models\Realtime;

enum VZapsEventType: string
{
    case Message = 'Message';
    case ReadReceipt = 'ReadReceipt';
    case Presence = 'Presence';
    case HistorySync = 'HistorySync';
    case ChatPresence = 'ChatPresence';
    case Connected = 'Connected';
    case Disconnected = 'Disconnected';
    case GroupParticipantsAdd = 'GroupParticipantsAdd';
    case GroupParticipantsRemove = 'GroupParticipantsRemove';
    case All = 'All';
}
